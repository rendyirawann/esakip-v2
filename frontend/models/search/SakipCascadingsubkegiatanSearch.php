<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipCascadingsubkegiatan;

/**
 * SakipCascadingsubkegiatanSearch represents the model behind the search form of `frontend\models\SakipCascadingsubkegiatan`.
 */
class SakipCascadingsubkegiatanSearch extends SakipCascadingsubkegiatan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refcascadingsubkegiatan_id', 'refcascadingkegiatan_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refcascadingprogram_id', 'refprogram_id', 'refkegiatan_id', 'refsubkegiatan_id', 'refperiode_id', 'refskpd_id'], 'integer'],
            [['subkegiatan_target', 'subkegiatan_satuan', 'subkegiatan_anggaran'], 'safe'],
            [['uraian_sasaransubkegiatan', 'uraian_indikatorsubkegiatan'], 'string'],
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
        $query = SakipCascadingsubkegiatan::find();

        // Eager load relations for performance
        $query->with(['refSubkegiatan', 'refKegiatan', 'refProgram', 'refPeriode']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->query->orderBy(['refsasaranrenstra_id' => SORT_ASC, 'refsubkegiatan_id' => SORT_ASC]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refcascadingsubkegiatan_id' => $this->refcascadingsubkegiatan_id,
            'refcascadingkegiatan_id' => $this->refcascadingkegiatan_id,
            'refcascadingprogram_id' => $this->refcascadingprogram_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refindikatorsasaranrenstra_id' => $this->refindikatorsasaranrenstra_id,
            'refprogram_id' => $this->refprogram_id,
            'refkegiatan_id' => $this->refkegiatan_id,
            'refsubkegiatan_id' => $this->refsubkegiatan_id,
            'refperiode_id' => $this->refperiode_id,
            'refskpd_id' => $this->refskpd_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_sasaransubkegiatan', $this->uraian_sasaransubkegiatan])
            ->andFilterWhere(['like', 'uraian_indikatorsubkegiatan', $this->uraian_indikatorsubkegiatan])
            ->andFilterWhere(['like', 'subkegiatan_target', $this->subkegiatan_target])
            ->andFilterWhere(['like', 'subkegiatan_satuan', $this->subkegiatan_satuan])
            ->andFilterWhere(['like', 'subkegiatan_anggaran', $this->subkegiatan_anggaran]);

        return $dataProvider;
    }
}
