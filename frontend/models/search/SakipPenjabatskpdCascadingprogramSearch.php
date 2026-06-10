<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipPenjabatskpdCascadingprogram;

/**
 * SakipPenjabatskpdCascadingprogramSearch represents the model behind the search form of `frontend\models\SakipPenjabatskpdCascadingprogram`.
 */
class SakipPenjabatskpdCascadingprogramSearch extends SakipPenjabatskpdCascadingprogram
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refpenjabatcascadingprogram_id', 'refpenjabatskpd_id', 'refeselon_id', 'refcascadingprogram_id', 'refindikatorprogram_id', 'refskpd_id', 'refperiode_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refbidang_id', 'refprogram_id'], 'integer'],
            [['uraian_sasaranprogram', 'uraian_indikatorprogram', 'program_target', 'program_satuan', 'target_rkt', 'target_rkt_p', 'target_pk', 'target_pk_p', 'realisasi', 'capaian', 'keterangan', 'analisis'], 'safe'],
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
        $query = SakipPenjabatskpdCascadingprogram::find();

        // Eager load relations for performance
        $query->with(['refProgram', 'refPeriode']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refpenjabatcascadingprogram_id' => $this->refpenjabatcascadingprogram_id,
            'refpenjabatskpd_id' => $this->refpenjabatskpd_id,
            'refeselon_id' => $this->refeselon_id,
            'refcascadingprogram_id' => $this->refcascadingprogram_id,
            'refindikatorprogram_id' => $this->refindikatorprogram_id,
            'refskpd_id' => $this->refskpd_id,
            'refperiode_id' => $this->refperiode_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refindikatorsasaranrenstra_id' => $this->refindikatorsasaranrenstra_id,
            'refbidang_id' => $this->refbidang_id,
            'refprogram_id' => $this->refprogram_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_sasaranprogram', $this->uraian_sasaranprogram])
            ->andFilterWhere(['like', 'uraian_indikatorprogram', $this->uraian_indikatorprogram])
            ->andFilterWhere(['like', 'program_target', $this->program_target])
            ->andFilterWhere(['like', 'program_satuan', $this->program_satuan])
            ->andFilterWhere(['like', 'target_rkt', $this->target_rkt])
            ->andFilterWhere(['like', 'target_rkt_p', $this->target_rkt_p])
            ->andFilterWhere(['like', 'target_pk', $this->target_pk])
            ->andFilterWhere(['like', 'target_pk_p', $this->target_pk_p])
            ->andFilterWhere(['like', 'realisasi', $this->realisasi])
            ->andFilterWhere(['like', 'capaian', $this->capaian])
            ->andFilterWhere(['like', 'keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'analisis', $this->analisis]);

        return $dataProvider;
    }
}
