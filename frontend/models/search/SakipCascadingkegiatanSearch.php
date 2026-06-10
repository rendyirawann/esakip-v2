<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipCascadingkegiatan;

/**
 * SakipCascadingkegiatanSearch represents the model behind the search form of `frontend\models\SakipCascadingkegiatan`.
 */
class SakipCascadingkegiatanSearch extends SakipCascadingkegiatan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refcascadingkegiatan_id', 'refcascadingprogram_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refprogram_id', 'refkegiatan_id', 'refperiode_id', 'refskpd_id'], 'integer'],
            // [['uraian_sasarankegiatan', 'uraian_indikatorkegiatan', 'kegiatan_target', 'kegiatan_satuan'], 'safe'],
            [['kegiatan_target', 'kegiatan_satuan'], 'safe'],
            [['uraian_sasarankegiatan', 'uraian_indikatorkegiatan'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SakipCascadingkegiatan::find();

        // Eager load relations for performance
        $query->with(['refProgram', 'refKegiatan', 'refPeriode']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->query->orderBy(['refsasaranrenstra_id' => SORT_ASC, 'refkegiatan_id' => SORT_ASC]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refcascadingkegiatan_id' => $this->refcascadingkegiatan_id,
            'refcascadingprogram_id' => $this->refcascadingprogram_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refindikatorsasaranrenstra_id' => $this->refindikatorsasaranrenstra_id,
            'refprogram_id' => $this->refprogram_id,
            'refkegiatan_id' => $this->refkegiatan_id,
            'refperiode_id' => $this->refperiode_id,
            'refskpd_id' => $this->refskpd_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_sasarankegiatan', $this->uraian_sasarankegiatan])
            ->andFilterWhere(['like', 'uraian_indikatorkegiatan', $this->uraian_indikatorkegiatan])
            ->andFilterWhere(['like', 'kegiatan_target', $this->kegiatan_target])
            ->andFilterWhere(['like', 'kegiatan_satuan', $this->kegiatan_satuan]);

        return $dataProvider;
    }
}
