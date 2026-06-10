<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipCascadingprogram;

/**
 * SakipCascadingprogramSearch represents the model behind the search form of `frontend\models\SakipCascadingprogram`.
 */
class SakipCascadingprogramSearch extends SakipCascadingprogram
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refcascadingprogram_id', 'refsasaran_id', 'refskpd_id', 'reftujuan_id', 'refmisi_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refbidang_id', 'refprogram_id', 'refperiode_id'], 'integer'],
            [['program_target', 'program_satuan'], 'safe'],
            [['uraian_sasaranprogram', 'uraian_indikatorprogram'], 'string'],
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
        $query = SakipCascadingprogram::find();

        // Eager load relations for performance
        $query->with(['refProgram', 'refPeriode']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->query->orderBy(['refsasaranrenstra_id' => SORT_ASC, 'refprogram_id' => SORT_ASC]);


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refcascadingprogram_id' => $this->refcascadingprogram_id,
            'refsasaran_id' => $this->refsasaran_id,
            'refskpd_id' => $this->refskpd_id,
            'reftujuan_id' => $this->reftujuan_id,
            'refmisi_id' => $this->refmisi_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refindikatorsasaranrenstra_id' => $this->refindikatorsasaranrenstra_id,
            'refbidang_id' => $this->refbidang_id,
            'refprogram_id' => $this->refprogram_id,
            'refperiode_id' => $this->refperiode_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_sasaranprogram', $this->uraian_sasaranprogram])
            ->andFilterWhere(['like', 'uraian_indikatorprogram', $this->uraian_indikatorprogram])
            ->andFilterWhere(['like', 'program_target', $this->program_target])
            ->andFilterWhere(['like', 'program_satuan', $this->program_satuan]);

        return $dataProvider;
    }
}
